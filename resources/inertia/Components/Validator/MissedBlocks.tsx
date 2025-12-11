import { IValidator } from "@/types/generated";
import Badge from "@/Components/General/Badge";
import classNames from "classnames";

export default function MissedBlocks({ validator }: { validator: IValidator }) {
    return (
        <Badge
            className="min-w-[30px] text-center"
            colors={classNames({
                "border-theme-success-100 bg-theme-success-100 text-theme-success-700 dark:border-theme-success-700 dark:text-theme-success-500":
                    validator.missedBlocksState === "success",
                "border-theme-orange-light bg-theme-orange-light text-theme-orange-dark dim:text-theme-warning-400 dark:!border-theme-warning-600 dark:text-theme-warning-400":
                    validator.missedBlocksState === "warning",
                "border-theme-danger-100 bg-theme-danger-100 text-theme-danger-700 dim:border-theme-failed-state-bg dim:text-theme-failed-state-text dark:border-theme-failed-state-bg dark:text-theme-failed-state-text":
                    validator.missedBlocksState === "danger",
                "encapsulated-badge border-transparent bg-theme-secondary-200 dark:border-theme-dark-800 dark:text-theme-dark-500":
                    validator.missedBlocksState === "inactive",
            })}
        >
            {validator.missedBlocks}
        </Badge>
    );
}
