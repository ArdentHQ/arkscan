export default function VoteTooltipContent({ variant, validator }: { variant: "vote" | "voting"; validator: string }) {
    return (
        <span className="break-words font-semibold text-theme-secondary-500">
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
