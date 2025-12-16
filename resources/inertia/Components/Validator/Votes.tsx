import { IValidator } from "@/types/generated";
import Tooltip from "../General/Tooltip";
import { currencyWithDecimals } from "@/utils/number-formatter";
import useSharedData from "@/hooks/use-shared-data";
import Number from "@/Components/General/Number";

export default function Votes({ validator }: { validator: IValidator }) {
    const { network } = useSharedData();
    const votes = validator.votes;

    return (
        <>
            {votes > 0 && votes < 0.01 ? (
                <Tooltip
                    content={currencyWithDecimals({
                        value: votes,
                        currency: network!.currency,
                        hideCurrency: true,
                    })}
                >
                    <span>&lt;0.01</span>
                </Tooltip>
            ) : (
                <Number>{votes}</Number>
            )}
        </>
    );
}
