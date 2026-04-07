import "./bootstrap";

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
        isFullscreen: false,

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
            if (this.initialized) return;
            this.initialized = true;
            const container = document.getElementById("tactic-stage");
            const width = container.clientWidth;
            const height = container.clientHeight;

            this.stage = new Konva.Stage({
                container: "tactic-stage",
                width,
                height,
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
                    width,
                    height,
                });

                this.layer.add(bg);
                bg.moveToBottom();
                this.layer.draw();
            };

            fetch("/api/players")
                .then((res) => res.json())
                .then((players) => {
                    this.players = players;
                    this.selectedPlayerId = null;
                });

            document.addEventListener("fullscreenchange", () => {
                this.isFullscreen = !!document.fullscreenElement;
                setTimeout(() => this.resizeStage(), 300);
            });

            this.setupStageEvents();
        },

        setupStageEvents() {
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
                radius: 60,
                fill: color,
                opacity: 0.15,
            });

            // group.add(background);

            if (player.image_path) {
                const img = new Image();
                img.src = `/storage/${player.image_path}`;

                img.onload = () => {
                    const playerImg = new Konva.Image({
                        image: img,
                        width: 70,
                        height: 70,
                        x: 0,
                        y: 0,
                        cornerRadius: 60,
                    });

                    const playerName = new Konva.Text({
                        x: 0,
                        y: 75,
                        width: 70,
                        text: player.second_name,

                        fontSize: 17,
                        fontFamily: "Calibri",
                        fill: "white",
                        align: "center",
                    });

                    const deleteBtn = new Konva.Text({
                        x: 90,
                        y: 0,
                        text: "❌",
                        fontSize: 20,
                        fill: "red",
                        fontStyle: "bold",
                    });

                    deleteBtn.on("click", () => {
                        group.destroy();
                        this.playerGroups = this.playerGroups.filter(
                            (g) => g !== group
                        );
                        this.layer.draw();
                    });

                    deleteBtn.on("mouseover", () => {
                        document.body.style.cursor = "pointer";
                    });
                    deleteBtn.on("mouseout", () => {
                        document.body.style.cursor = "default";
                    });

                    group.add(playerImg);
                    group.add(deleteBtn);
                    group.add(playerName);

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
                    x: 20,
                    y: -25,
                    text: "❌",
                    fontSize: 18,
                    fill: "red",
                    fontStyle: "bold",
                });

                deleteBtn.on("click", () => {
                    group.destroy();
                    this.playerGroups = this.playerGroups.filter(
                        (g) => g !== group
                    );
                    this.layer.draw();
                });

                deleteBtn.on("mouseover", () => {
                    document.body.style.cursor = "pointer";
                });
                deleteBtn.on("mouseout", () => {
                    document.body.style.cursor = "default";
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

                deleteBtn.on("mouseover", () => {
                    document.body.style.cursor = "pointer";
                });
                deleteBtn.on("mouseout", () => {
                    document.body.style.cursor = "default";
                });

                group.add(ball);
                group.add(deleteBtn);
                this.layer.add(group);
                this.ballGroup = group;
                this.layer.draw();
            };
        },

        resizeStage() {
            const container = document.getElementById("tactic-stage");
            const width = container.clientWidth;
            const height = container.clientHeight;

            this.stage.width(width);
            this.stage.height(height);

            const bg = this.layer.findOne(
                (node) => node instanceof Konva.Image
            );
            if (bg) {
                bg.width(width);
                bg.height(height);
            }

            this.layer.draw();
        },

        toggleFullscreen() {
            const wrapper = document.getElementById("tactic-wrapper");

            if (!document.fullscreenElement) {
                wrapper
                    .requestFullscreen()
                    .then(() => {
                        this.isFullscreen = true;
                        setTimeout(() => this.resizeStage(), 300);
                    })
                    .catch((err) => {
                        alert(
                            `Error attempting to enable fullscreen: ${err.message}`
                        );
                    });
            } else {
                document.exitFullscreen().then(() => {
                    this.isFullscreen = false;
                    setTimeout(() => this.resizeStage(), 300);
                });
            }
        },

        downloadBoard(mode) {
            const logoImg = new Image();
            logoImg.src = "/images/logo/logo.jpg";

            logoImg.onload = () => {
                const padding = 16;
                const logoSize = 56;

                const logo = new Konva.Image({
                    image: logoImg,
                    x: this.stage.width() - logoSize - padding,
                    y: padding,
                    width: logoSize,
                    height: logoSize,
                    cornerRadius: logoSize / 2,
                });

                const label = new Konva.Text({
                    x: this.stage.width() - logoSize - padding - 140,
                    y: padding + 15,
                    fontSize: 18,
                    fontFamily: "Calibri",
                    fill: "white",
                    fontStyle: "bold",
                });

                this.layer.add(logo);
                this.layer.add(label);

                const deleteButtons = this.layer.find(
                    (node) => node instanceof Konva.Text && node.text() === "❌"
                );
                deleteButtons.forEach((btn) => btn.hide());

                this.layer.draw();

                setTimeout(() => {
                    if (mode === "vertical") {
                        this._downloadVertical(logo, label, deleteButtons, logoImg);
                    } else {
                        const dataURL = this.stage.toDataURL({ pixelRatio: 2 });

                        deleteButtons.forEach((btn) => btn.show());
                        logo.destroy();
                        label.destroy();
                        this.layer.draw();

                        const link = document.createElement("a");
                        link.download = "tactic-board.png";
                        link.href = dataURL;
                        link.click();
                    }
                }, 150);
            };
        },

        _downloadVertical(tempLogo, tempLabel, deleteButtons, logoImg) {
            const pr = 2;
            const sw = this.stage.width();
            const sh = this.stage.height();
            // Vertical canvas: swap width/height
            const cw = sh * pr;
            const ch = sw * pr;
            const canvas = document.createElement("canvas");
            canvas.width = cw;
            canvas.height = ch;
            const ctx = canvas.getContext("2d");

            // 1) Draw field background rotated 90° CW
            const fieldBg = this.layer.findOne(
                (node) =>
                    node instanceof Konva.Image &&
                    node !== tempLogo
            );
            if (fieldBg) {
                const fieldImg = fieldBg.image();
                ctx.save();
                ctx.translate(cw / 2, ch / 2);
                ctx.rotate(Math.PI / 2);
                ctx.drawImage(fieldImg, -ch / 2, -cw / 2, ch, cw);
                ctx.restore();
            }

            // Helper: convert horizontal (hx, hy) to vertical position
            // 90° CW rotation: new_x = sh - hy, new_y = hx
            const mapPos = (hx, hy) => ({
                x: (sh - hy) * pr,
                y: hx * pr,
            });

            // 2) Draw lines and arrows (remapped points)
            this.layer.getChildren().forEach((child) => {
                if (child.name() !== "drawing") return;
                if (
                    child instanceof Konva.Line ||
                    child instanceof Konva.Arrow
                ) {
                    const pts = child.points();
                    ctx.save();
                    ctx.strokeStyle = child.stroke();
                    ctx.lineWidth = child.strokeWidth() * pr;
                    ctx.lineCap = "round";
                    ctx.lineJoin = "round";
                    ctx.beginPath();
                    for (let i = 0; i < pts.length; i += 2) {
                        const { x, y } = mapPos(pts[i], pts[i + 1]);
                        if (i === 0) ctx.moveTo(x, y);
                        else ctx.lineTo(x, y);
                    }
                    ctx.stroke();

                    // Draw arrowhead for Arrow shapes
                    if (child instanceof Konva.Arrow && pts.length >= 4) {
                        const p1 = mapPos(
                            pts[pts.length - 4],
                            pts[pts.length - 3]
                        );
                        const p2 = mapPos(
                            pts[pts.length - 2],
                            pts[pts.length - 1]
                        );
                        const angle = Math.atan2(
                            p2.y - p1.y,
                            p2.x - p1.x
                        );
                        const headLen = child.pointerLength() * pr;
                        ctx.fillStyle = child.fill() || child.stroke();
                        ctx.beginPath();
                        ctx.moveTo(p2.x, p2.y);
                        ctx.lineTo(
                            p2.x - headLen * Math.cos(angle - Math.PI / 6),
                            p2.y - headLen * Math.sin(angle - Math.PI / 6)
                        );
                        ctx.lineTo(
                            p2.x - headLen * Math.cos(angle + Math.PI / 6),
                            p2.y - headLen * Math.sin(angle + Math.PI / 6)
                        );
                        ctx.closePath();
                        ctx.fill();
                    }
                    ctx.restore();
                }
            });

            // 3) Draw player groups upright with rounded images + name below
            // Scale up players for vertical export
            const vertScale = 1.5;

            const drawGroupUpright = (group) => {
                const gx = group.x();
                const gy = group.y();

                // Find the children by type
                let imgChild = null;
                let textChild = null;
                let circleChild = null;
                let numberChild = null;

                group.getChildren().forEach((child) => {
                    if (child instanceof Konva.Text && child.text() === "❌") return;
                    if (child instanceof Konva.Image) imgChild = child;
                    else if (child instanceof Konva.Circle) circleChild = child;
                    else if (child instanceof Konva.Text) {
                        if (textChild) numberChild = child;
                        else textChild = child;
                    }
                });

                // For image-based players (with photo)
                if (imgChild) {
                    const img = imgChild.image();
                    if (!img) return;
                    const size = imgChild.width() * pr * vertScale;
                    // Center of the group in horizontal coords
                    const centerHx = gx + imgChild.x() + imgChild.width() / 2;
                    const centerHy = gy + imgChild.y() + imgChild.height() / 2;
                    const { x: cx, y: cy } = mapPos(centerHx, centerHy);

                    // Draw rounded player image
                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(cx, cy, size / 2, 0, Math.PI * 2);
                    ctx.closePath();
                    ctx.clip();
                    ctx.drawImage(img, cx - size / 2, cy - size / 2, size, size);
                    ctx.restore();

                    // Draw name below the image
                    if (textChild) {
                        const fontSize = textChild.fontSize() * pr * vertScale;
                        ctx.save();
                        ctx.font = `${fontSize}px ${textChild.fontFamily()}`;
                        ctx.fillStyle = textChild.fill();
                        ctx.textAlign = "center";
                        ctx.fillText(textChild.text(), cx, cy + size / 2 + fontSize + 4);
                        ctx.restore();
                    }
                }
                // For circle-based players (no photo, just number)
                else if (circleChild) {
                    const centerHx = gx + circleChild.x();
                    const centerHy = gy + circleChild.y();
                    const { x: cx, y: cy } = mapPos(centerHx, centerHy);
                    const r = circleChild.radius() * pr * vertScale;

                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(cx, cy, r, 0, Math.PI * 2);
                    ctx.fillStyle = circleChild.fill();
                    ctx.fill();
                    if (circleChild.stroke()) {
                        ctx.strokeStyle = circleChild.stroke();
                        ctx.lineWidth = (circleChild.strokeWidth() || 1) * pr;
                        ctx.stroke();
                    }
                    ctx.restore();

                    // Draw the number inside the circle
                    if (textChild) {
                        ctx.save();
                        ctx.font = `${textChild.fontSize() * pr * vertScale}px ${textChild.fontFamily()}`;
                        ctx.fillStyle = textChild.fill();
                        ctx.textAlign = "center";
                        ctx.textBaseline = "middle";
                        ctx.fillText(textChild.text(), cx, cy);
                        ctx.restore();
                    }
                }
                // For ball group (just an image, no text)
                else {
                    group.getChildren().forEach((child) => {
                        if (child instanceof Konva.Text && child.text() === "❌") return;
                        if (child instanceof Konva.Image) {
                            const img = child.image();
                            if (!img) return;
                            const w = child.width() * pr;
                            const h = child.height() * pr;
                            const hx = gx + child.x() + child.width() / 2;
                            const hy = gy + child.y() + child.height() / 2;
                            const { x, y } = mapPos(hx, hy);
                            ctx.drawImage(img, x - w / 2, y - h / 2, w, h);
                        }
                    });
                }
            };

            this.playerGroups.forEach((g) => drawGroupUpright(g));
            if (this.ballGroup) drawGroupUpright(this.ballGroup);

            // 4) Draw logo in top-right corner of the vertical image
            const logoPad = 16 * pr;
            const logoSz = 56 * pr;
            ctx.drawImage(
                logoImg,
                cw - logoSz - logoPad,
                logoPad,
                logoSz,
                logoSz
            );

            // Cleanup temp Konva nodes
            deleteButtons.forEach((btn) => btn.show());
            tempLogo.destroy();
            tempLabel.destroy();
            this.layer.draw();

            // Download
            const link = document.createElement("a");
            link.download = "tactic-board-vertical.png";
            link.href = canvas.toDataURL("image/png");
            link.click();
        },

        onPlayerSelected(event) {
            if (this.tool !== null) return;

            const playerId = event.target.value;
            const player = this.players.find((p) => p.id == playerId);

            if (player) {
                const x = this.stage.width() / 2;
                const y = this.stage.height() / 2;
                this.addPlayerToBoard(player, x, y);
                this.selectedPlayerId = null;
                event.target.value = "";
            }
        },
    };
};

document.addEventListener("alpine:init", () => {
    Livewire.hook("element.removed", (el) => {
        const alpineComponents = el.querySelectorAll("[x-data]");
        alpineComponents.forEach((comp) => {
            if (
                comp._x_dataStack &&
                typeof comp._x_dataStack[0]?.destroy === "function"
            ) {
                comp._x_dataStack[0].destroy();
            }
        });
    });
});
