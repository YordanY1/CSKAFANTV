import Konva from "konva";

window.tacticBoard = function () {
    return {
        stage: null,
        layer: null,
        players: [],
        selectedPlayerId: null,
        playerGroups: [],
        tool: null,
        isDrawing: false,
        currentLine: null,
        arrowStart: null,
        ballGroup: null,

        get selectedPlayer() {
            return (
                this.players.find((p) => p.id == this.selectedPlayerId) || null
            );
        },

        setTool(tool) {
            this.tool = tool;
            this.selectedPlayerId = null;
        },

        init() {
            this.stage = new Konva.Stage({
                container: "tactic-stage",
                width: 1104,
                height: 596,
            });

            this.layer = new Konva.Layer();
            this.stage.add(this.layer);

            const imageObj = new Image();
            imageObj.src = "/images/field.jpg";

            imageObj.onload = () => {
                const bg = new Konva.Image({
                    x: 0,
                    y: 0,
                    image: imageObj,
                    width: 1104,
                    height: 596,
                });

                this.layer.add(bg);
                this.layer.draw();
            };

            fetch("/api/players")
                .then((res) => res.json())
                .then((players) => {
                    this.players = players;
                    this.selectedPlayerId = null;
                });

            this.stage.on("mousedown touchstart", (e) => {
                const pos = this.stage.getPointerPosition();

                if (this.tool === "draw") {
                    this.isDrawing = true;
                    this.currentLine = new Konva.Line({
                        points: [pos.x, pos.y],
                        stroke: "#000",
                        strokeWidth: 3,
                        lineCap: "round",
                        lineJoin: "round",
                        name: "drawing",
                    });
                    this.layer.add(this.currentLine);
                }

                if (this.tool === "arrow") {
                    this.arrowStart = pos;
                }

                if (this.tool === "eraser") {
                    const shape = e.target;
                    if (
                        shape &&
                        (shape.className === "Line" ||
                            shape.className === "Arrow") &&
                        shape.name() === "drawing"
                    ) {
                        shape.destroy();
                        this.layer.draw();
                    }
                }
            });

            this.stage.on("mousemove touchmove", () => {
                if (!this.isDrawing || this.tool !== "draw") return;

                const pos = this.stage.getPointerPosition();
                const newPoints = this.currentLine
                    .points()
                    .concat([pos.x, pos.y]);
                this.currentLine.points(newPoints);
                this.layer.batchDraw();
            });

            this.stage.on("mouseup touchend", () => {
                if (this.tool === "draw") {
                    this.isDrawing = false;
                }

                if (this.tool === "arrow" && this.arrowStart) {
                    const end = this.stage.getPointerPosition();
                    const arrow = new Konva.Arrow({
                        points: [
                            this.arrowStart.x,
                            this.arrowStart.y,
                            end.x,
                            end.y,
                        ],
                        stroke: "#000",
                        fill: "#000",
                        strokeWidth: 3,
                        pointerLength: 10,
                        pointerWidth: 10,
                        name: "drawing",
                    });
                    this.layer.add(arrow);
                    this.arrowStart = null;
                    this.layer.draw();
                }
            });

            this.stage.on("click", () => {
                const { x, y } = this.stage.getPointerPosition();

                if (this.tool === "ball") {
                    this.addBall(x, y);
                    return;
                }

                if (this.selectedPlayerId && this.tool === null) {
                    const player = this.selectedPlayer;
                    this.addPlayerToBoard(player, x, y);
                }
            });
        },

        clearBoard() {
            this.playerGroups.forEach((group) => group.destroy());
            this.playerGroups = [];

            if (this.ballGroup) {
                this.ballGroup.destroy();
                this.ballGroup = null;
            }

            this.layer.getChildren().each((child) => {
                if (!(child instanceof Konva.Image)) child.destroy();
            });
            this.layer.draw();
        },

        getColorByPosition(position) {
            const pos = (position || "").toLowerCase();
            if (pos.includes("gk")) return "#facc15";
            if (pos.includes("def")) return "#60a5fa";
            if (pos.includes("mid")) return "#34d399";
            if (pos.includes("att") || pos.includes("fw")) return "#f87171";
            return "#a3a3a3";
        },

        addPlayerToBoard(player, x, y) {
            const group = new Konva.Group({
                x: x - 40,
                y: y - 40,
                draggable: true,
            });

            const color = this.getColorByPosition(player.position);

            const background = new Konva.Circle({
                x: 40,
                y: 40,
                radius: 45,
                fill: color,
                opacity: 0.15,
            });

            group.add(background);

            if (player.image_path) {
                const img = new Image();
                img.src = `/storage/${player.image_path}`;

                img.onload = () => {
                    const playerImg = new Konva.Image({
                        image: img,
                        width: 80,
                        height: 80,
                    });

                    const playerName = new Konva.Text({
                        y: 85,
                        text: player.name,
                        fontSize: 14,
                        fontFamily: "Calibri",
                        fill: "white",
                        align: "center",
                        width: 80,
                    });

                    const deleteBtn = new Konva.Text({
                        x: 65,
                        y: -10,
                        text: "❌",
                        fontSize: 16,
                        fill: "red",
                        fontStyle: "bold",
                        width: 20,
                        height: 20,
                    });

                    deleteBtn.on("click", () => {
                        group.destroy();
                        this.layer.draw();
                    });

                    group.add(playerImg);
                    group.add(playerName);
                    group.add(deleteBtn);

                    this.layer.add(group);
                    this.playerGroups.push(group);
                    this.layer.draw();
                };
            } else {
                const circle = new Konva.Circle({
                    radius: 30,
                    fill: color,
                    stroke: "white",
                    strokeWidth: 2,
                });

                const text = new Konva.Text({
                    x: -10,
                    y: -10,
                    text: player.number ? player.number.toString() : "?",
                    fontSize: 20,
                    fontFamily: "Calibri",
                    fill: "white",
                });

                const deleteBtn = new Konva.Text({
                    x: 35,
                    y: -25,
                    text: "❌",
                    fontSize: 16,
                    fill: "red",
                    fontStyle: "bold",
                });

                deleteBtn.on("click", () => {
                    group.destroy();
                    this.layer.draw();
                });

                group.add(circle);
                group.add(text);
                group.add(deleteBtn);

                this.layer.add(group);
                this.playerGroups.push(group);
                this.layer.draw();
            }
        },

        addBall(x, y) {
            if (this.ballGroup) {
                this.ballGroup.destroy();
                this.ballGroup = null;
            }

            const img = new Image();
            img.src = "/images/ball.png";

            img.onload = () => {
                const ball = new Konva.Image({
                    image: img,
                    width: 40,
                    height: 40,
                });

                const deleteBtn = new Konva.Text({
                    x: 25,
                    y: -10,
                    text: "❌",
                    fontSize: 16,
                    fill: "red",
                    fontStyle: "bold",
                });

                const group = new Konva.Group({
                    x: x - 20,
                    y: y - 20,
                    draggable: true,
                });

                deleteBtn.on("click", () => {
                    group.destroy();
                    this.ballGroup = null;
                    this.layer.draw();
                });

                group.add(ball);
                group.add(deleteBtn);
                this.layer.add(group);
                this.ballGroup = group;
                this.layer.draw();
            };
        },
    };
};
