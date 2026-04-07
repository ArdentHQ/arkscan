import { useTranslation } from "react-i18next";

import { PeerGroup } from "./WorldMap";

export default function PeerTooltip({
    group,
    x,
    y,
    flipped,
}: {
    group: PeerGroup;
    x: number;
    y: number;
    flipped: boolean;
}) {
    const { t } = useTranslation();

    return (
        <div
            className="pointer-events-none absolute z-10 rounded-lg border border-theme-secondary-300 bg-white px-3 py-2 text-sm shadow-xl dark:border-theme-dark-700 dark:bg-theme-dark-900"
            style={{
                left: x,
                top: y,
                transform: flipped ? "translate(-50%, 12px)" : "translate(-50%, -100%) translateY(-12px)",
            }}
        >
            <div className="space-y-0.5">
                <div className="font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                    {group.count === 1 ? t("pages.peers-map.peer") : t("pages.peers-map.peers", { count: group.count })}
                </div>

                {group.location && (
                    <div className="text-theme-secondary-500 dark:text-theme-dark-300">{group.location}</div>
                )}
            </div>
        </div>
    );
}
