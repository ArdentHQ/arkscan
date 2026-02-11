import { Link } from "@inertiajs/react";
import TruncateMiddle from "../General/TruncateMiddle";
import classNames from "classnames";
import { IMemoryWallet, IWallet } from "@/types/generated";

export default function Address({
    wallet,
    truncate = false,
    className = "",
}: {
    wallet: IWallet | IMemoryWallet;
    truncate?: boolean | number;
    className?: string;
}) {
    let name: string | undefined;
    if ("attributes" in wallet) {
        name = wallet?.attributes?.username;
    } else {
        name = wallet?.username || undefined;
    }

    return (
        <div
            className={classNames({
                "min-w-0": true,
                [className]: true,
            })}
        >
            <div className="min-w-0 truncate">
                <Link className="link whitespace-nowrap" href={route("wallet", wallet.address)}>
                    {!!name ? (
                        name
                    ) : (
                        <>
                            {truncate === true && <TruncateMiddle>{wallet.address}</TruncateMiddle>}

                            {typeof truncate === "number" && (
                                <TruncateMiddle length={truncate}>{wallet.address}</TruncateMiddle>
                            )}

                            {truncate === false && wallet.address}
                        </>
                    )}
                </Link>
            </div>
        </div>
    );
}
