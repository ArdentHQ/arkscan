import { Link } from "@inertiajs/react";

export default function Address({ wallet }) {
    const name = wallet?.attributes?.username || wallet.address;

    return (
        <div className="min-w-0">
            <div className="min-w-0 truncate">
                <Link
                    className="whitespace-nowrap link"
                    href={`/addresses/${wallet.address}`}
                >
                    {name}
                </Link>
            </div>
        </div>
    );
}
