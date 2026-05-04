import { IToken } from "@/types/generated";
import { Link } from "@inertiajs/react";
import classNames from "classnames";
import TruncatedValue from "./TruncatedValue";

export default function Token({ token, className }: { token: IToken; className?: string }) {
    return (
        <div
            className={classNames([
                "text-theme-primary-600 hover:text-theme-primary-700 dim:text-theme-dark-blue-600 dark:text-theme-dark-blue-400 dark:hover:text-theme-dark-blue-500 truncate",
                className,
            ])}
        >
            <Link href={route("wallet", { wallet: token.address })} className="link">
                <TruncatedValue value={token.name} />
            </Link>
        </div>
    );
}
