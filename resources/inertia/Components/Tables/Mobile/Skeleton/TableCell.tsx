import LoadingText from "@/Components/Loading/Text";
import classNames from "classnames";

export default function LoadingTableCell({
    withLabel = false,
    className = "",
}: {
    withLabel?: boolean;
    className?: string;
}) {
    return (
        <div
            className={classNames({
                "flex flex-col space-y-2 leading-4.25 font-semibold": true,
                [className]: true,
            })}
        >
            {withLabel && <LoadingText />}

            <LoadingText />
        </div>
    );
}
