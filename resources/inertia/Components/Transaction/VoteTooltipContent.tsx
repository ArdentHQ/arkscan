export default function VoteTooltipContent({ variant, validator }: { variant: "vote" | "voting"; validator: string }) {
    return (
        <span className="text-theme-secondary-500 font-semibold break-words">
            {variant === "vote" ? (
                <>
                    <span>Vote:</span>
                    <span className="ml-1 text-white">{validator}</span>
                </>
            ) : (
                <>
                    <span>Voting for </span>
                    <span className="text-white">{validator}</span>
                </>
            )}
        </span>
    );
}
