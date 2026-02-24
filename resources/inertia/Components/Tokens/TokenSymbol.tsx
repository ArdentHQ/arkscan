import Tooltip from "../General/Tooltip";

export default function TokenSymbol({
    tokenSymbol,
    fullTokenSymbol,    
    addSpace = true,
}: {
    tokenSymbol?: string;
    fullTokenSymbol?: string;
    addSpace?: boolean;
}) {
    if (tokenSymbol === undefined) {
        return <></>
    }

    const showTooltip = fullTokenSymbol !== undefined && tokenSymbol !== null && fullTokenSymbol !== tokenSymbol;

    if (showTooltip) {
        return <Tooltip className="inline" content={`${fullTokenSymbol}`}><span>{addSpace ? <>&nbsp;{tokenSymbol}</> : tokenSymbol}</span></Tooltip>
    }

    return <span>{addSpace ? <>&nbsp;{tokenSymbol}</> : tokenSymbol}</span>
   
}
