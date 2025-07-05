import axios from "axios";
import Konva from "konva";
import { DateTime } from "luxon";

window.axios = axios;
window.Konva = Konva;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const dt = DateTime.now().setZone("Europe/Sofia");

window.matchCountdown = function ({
    matchTime,
    isFinished = false,
    youtube = "",
}) {
    return {
        label: "",
        isLive: false,
        youtubeUrl: youtube,
        interval: null,

        init() {
            const start = DateTime.fromISO(matchTime, { zone: "Europe/Sofia" });

            const update = () => {
                const now = DateTime.now().setZone("Europe/Sofia");
                const end = start.plus({ hours: 2 });
                const extended = end.plus({ minutes: 30 });

                if (isFinished) {
                    this.label = "✅ Приключил";
                    this.isLive = false;
                } else if (now >= start && now < extended) {
                    this.label = "🔴 В ефир";
                    this.isLive = true;
                } else if (now < start) {
                    const diff = start
                        .diff(now, ["hours", "minutes"])
                        .toObject();
                    const hours = Math.floor(diff.hours || 0);
                    const minutes = Math.floor(diff.minutes || 0);
                    this.label = `⏳ Започва след ${
                        hours ? `${hours} ч. ` : ""
                    }${minutes} мин.`;
                    this.isLive = false;
                } else {
                    this.label = "✅ Приключил";
                    this.isLive = false;
                }
            };

            update();
            this.interval = setInterval(update, 60000);
        },

        destroy() {
            clearInterval(this.interval);
        },
    };
};
