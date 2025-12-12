import { IValidator } from "@/types";

export default function BlockHeight({ validator }: { validator: IValidator }) {
    if (validator.wallet?.hasForged && validator.lastBlock?.number !== undefined) {
        return (
            <a href={`/blocks/${validator?.lastBlock?.hash}`} className="link">
                {validator.lastBlock?.number.toLocaleString()}
            </a>
        );
    }

    return (
        <span className="text-theme-secondary-500 dark:text-theme-dark-500">
            {validator.wallet.justMissed ? "N/A" : "TBD"}
        </span>
    );
}
