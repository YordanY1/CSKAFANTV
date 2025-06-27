import axios from "axios";
import Konva from "konva";
import { DateTime } from "luxon";

window.axios = axios;
window.Konva = Konva;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const dt = DateTime.now().setZone("Europe/Sofia");
console.log(dt.toFormat("dd LLL yyyy, HH:mm"));

window.matchCountdown = function (matchTime, isFinished = false, youtube = "") {
    return {
        label: "",
        color: "",
        isLive: false,
        youtubeUrl: youtube,

        init() {
            const start = DateTime.fromISO(matchTime, { zone: "Europe/Sofia" });

            const update = () => {
                const now = DateTime.now().setZone("Europe/Sofia");
                const end = start.plus({ hours: 2 });
                const extended = end.plus({ minutes: 30 });

                if (isFinished) {
                    this.label = "✅ Приключил";
                    this.color = "text-gray-500";
                    this.isLive = false;
                    return;
                }

                if (now >= start && now < extended) {
                    this.label = "🔴 В ефир";
                    this.color = "text-red-600 animate-pulse font-semibold";
                    this.isLive = true;
                } else if (now < start) {
                    const diff = start
                        .diff(now, ["hours", "minutes"])
                        .toObject();
                    const hours = Math.floor(diff.hours || 0);
                    const minutes = Math.floor(diff.minutes || 0);
                    this.label = `⏳ Започва след ${
                        hours > 0 ? hours + " ч. " : ""
                    }${minutes} мин.`;
                    this.color = "text-yellow-600";
                    this.isLive = false;
                } else {
                    this.label = "✅ Приключил";
                    this.color = "text-gray-400";
                    this.isLive = false;
                }
            };

            update();
            setInterval(update, 60000);
        },
    };
};
