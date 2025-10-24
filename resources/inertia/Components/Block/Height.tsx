import { IBlock } from "@/types/generated";
import Age from "../Model/Age";

export default function Height({
    block,
    withoutLink = false,
}: {
    block: IBlock;
    withoutLink?: boolean;
}) {
    const formattedBlockHeight = Intl.NumberFormat().format(block.number);

    return (
        <div className="text-sm font-semibold flex flex-col md:space-y-1 xl:space-y-0 whitespace-nowrap leading-4.25 text-theme-secondary-900 dark:text-theme-dark-50">
            {withoutLink ? (
                <span>{formattedBlockHeight}</span>
            ) : (
                <a href={`/blocks/${block.hash}`} className="link">
                    {formattedBlockHeight}
                </a>
            )}

            <Age
                timestamp={block.timestamp}
                className="hidden text-xs md:block leading-3.75 text-theme-secondary-700 md-lg:hidden dark:text-theme-dark-200"
            />
        </div>
    );
}
