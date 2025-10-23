import LoadingText from "@/Components/Loading/Text";
import classNames from "../../../../utils/class-names";

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
                "flex flex-col space-y-2 font-semibold leading-4.25": true,
                [className]: true,
            })}
        >
            {withLabel && <LoadingText />}

            <LoadingText />
        </div>
    );
}
