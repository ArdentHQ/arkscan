import { IWallet } from "@/types/generated";
import TruncateMiddle from "./TruncateMiddle";
import classNames from "classnames";
import { Link } from "@inertiajs/react";

// Notice that the original blade identity component has a lot of extra
// logic that we may need in the future.
export default function Identity({
    model,
    address: addressProp,
    className,
    ...props
}: React.HTMLAttributes<HTMLDivElement> &
    React.PropsWithChildren<{
        model?: Pick<IWallet, "address" | "username" | "hasUsername">;
        address?: string;
    }>) {
    const address = model?.address || addressProp;

    const hasUsername = model?.hasUsername || false;

    return (
        <div className={classNames("flex items-center md:flex-row md:justify-start", className)} {...props}>
            <div className="flex items-center md:mr-0">
                <Link href={route("wallet", address)} className="link font-semibold sm:hidden md:flex">
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
