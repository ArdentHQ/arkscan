import { IBlock } from "@/types/generated";
import Age from "../Model/Age";

export default function Height({ block, withoutLink = false }: { block: IBlock; withoutLink?: boolean }) {
    const formattedBlockHeight = Intl.NumberFormat().format(block.number);

    return (
        <div className="flex flex-col whitespace-nowrap text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-50 md:space-y-1 xl:space-y-0">
            {withoutLink ? (
                <span>{formattedBlockHeight}</span>
            ) : (
                <a href={`/blocks/${block.hash}`} className="link">
                    {formattedBlockHeight}
                </a>
            )}

            <Age
                timestamp={block.timestamp}
                className="hidden text-xs leading-3.75 text-theme-secondary-700 dark:text-theme-dark-200 md:block md-lg:hidden"
            />
        </div>
    );
}
