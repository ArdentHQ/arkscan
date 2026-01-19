import { IMonitorValidator } from "@/types";
import { Link } from "@inertiajs/react";

export default function BlockHeight({ validator }: { validator: IMonitorValidator }) {
    const lastBlock = validator.lastBlock;

    if (validator.wallet?.hasForged && lastBlock?.number !== undefined && lastBlock?.hash) {
        return (
            <Link href={route("block", lastBlock.hash)} className="link">
                {lastBlock.number.toLocaleString()}
            </Link>
        );
    }

    return (
        <span className="text-theme-secondary-500 dark:text-theme-dark-500">
            {validator.wallet.justMissed ? "N/A" : "TBD"}
        </span>
    );
}
