import { Link } from "@inertiajs/react";
import TruncateMiddle from "../General/TruncateMiddle";
import classNames from '../../utils/class-names';
import TruncateDynamic from "../TruncateDynamic";

export default function Address({ wallet, truncate = false, className = '' }: {
    wallet: any;
    truncate?: boolean | "dynamic";
    className?: string;
}) {
    const name = wallet?.attributes?.username;

    return (
        <div className={classNames({
            "min-w-0": true,
            [className]: true,
        })}>
            <div className="min-w-0 truncate">
                <Link
                    className="whitespace-nowrap link"
                    href={`/addresses/${wallet.address}`}
                >
                    {!!name ? name : (<>
                        {truncate === true && <TruncateMiddle length={5}>{wallet.address}</TruncateMiddle>}
                        {truncate === "dynamic" && <TruncateDynamic value={wallet.address} />}
                        {truncate === false && wallet.address}
                    </>)}
                </Link>
            </div>
        </div>
    );
}
