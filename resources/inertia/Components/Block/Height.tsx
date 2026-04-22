import { IBlock, IForgingStats } from "@/types/generated";
import { Link } from "@inertiajs/react";
import Age from "../Model/Age";

export default function Height({
    block,
    withoutLink = false,
}: {
    block: IBlock | IForgingStats;
    withoutLink?: boolean;
}) {
    const formattedBlockHeight = Intl.NumberFormat().format(block.number);

    return (
        <div className="text-theme-secondary-900 dark:text-theme-dark-50 flex flex-col text-sm leading-4.25 font-semibold whitespace-nowrap md:space-y-1 xl:space-y-0">
            {withoutLink ? (
                <span>{formattedBlockHeight}</span>
            ) : (
                <Link href={route("block", block.hash)} className="link">
                    {formattedBlockHeight}
                </Link>
            )}

            <Age
                timestamp={block.timestamp}
                className="text-theme-secondary-700 dark:text-theme-dark-200 md-lg:hidden! hidden text-xs leading-3.75 md:block"
            />
        </div>
    );
}
