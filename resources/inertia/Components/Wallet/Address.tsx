import { Link } from "@inertiajs/react";
import TruncateMiddle from "../General/TruncateMiddle";
import classNames from "classnames";
import { IMemoryWallet, IWallet } from "@/types/generated";

export default function Address({
    wallet,
    truncate = false,
    className = "",
}: {
    wallet: IWallet | IMemoryWallet | string;
    truncate?: boolean | number;
    className?: string;
}) {
    const address = typeof wallet === "string" ? wallet : wallet.address;

    let name: string | undefined;
    if (typeof wallet !== "string") {
        if ("attributes" in wallet) {
            name = wallet?.attributes?.username;
        } else {
            name = wallet?.username || undefined;
        }
    }

    return (
        <div
            className={classNames({
                "min-w-0": true,
                [className]: true,
            })}
        >
            <div className="min-w-0 truncate">
                <Link className="link whitespace-nowrap" href={route("wallet", address)}>
                    {!!name ? (
                        name
                    ) : (
                        <>
                            {truncate === true && <TruncateMiddle>{address}</TruncateMiddle>}

                            {typeof truncate === "number" && (
                                <TruncateMiddle length={truncate}>{address}</TruncateMiddle>
                            )}

                            {truncate === false && address}
                        </>
                    )}
                </Link>
            </div>
        </div>
    );
}
