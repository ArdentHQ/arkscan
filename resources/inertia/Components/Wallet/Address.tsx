import TruncateMiddle from "../General/TruncateMiddle";
import classNames from "../../utils/class-names";

export default function Address({
    wallet,
    truncate = false,
    className = "",
}: {
    wallet: any;
    truncate?: boolean | number;
    className?: string;
}) {
    const name = wallet?.attributes?.username;

    return (
        <div
            className={classNames({
                "min-w-0": true,
                [className]: true,
            })}
        >
            <div className="min-w-0 truncate">
                <a className="link whitespace-nowrap" href={`/addresses/${wallet.address}`}>
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
                </a>
            </div>
        </div>
    );
}
