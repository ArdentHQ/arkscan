import { IWallet } from "@/types/generated";
import TruncateMiddle from "./TruncateMiddle";
import classNames from "classnames";
import { Link } from "@inertiajs/react";

export default function Identity({
    model,
    address: addressProp,
    className,
    contentClassName,
    linkClassName,
    ...props
}: React.HTMLAttributes<HTMLDivElement> &
    React.PropsWithChildren<{
        model?: Pick<IWallet, "address" | "username" | "hasUsername">;
        address?: string;
        contentClassName?: string;
        linkClassName?: string;
    }>) {
    const address = model?.address || addressProp;

    const hasUsername = model?.hasUsername || false;

    return (
        <div className={classNames("flex items-center", className)} {...props}>
            <div className={classNames("flex items-center", contentClassName)}>
                <Link href={route("wallet", address)} className={classNames("link font-semibold", linkClassName)}>
                    {hasUsername ? (
                        <div className="validator-name-truncate">{model!.username!}</div>
                    ) : (
                        <TruncateMiddle>{address}</TruncateMiddle>
                    )}
                </Link>
            </div>
        </div>
    );
}
