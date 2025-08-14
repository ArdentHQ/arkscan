export default function BlockHeight({ validator }) {
    if (validator?.wallet?.hasForged) {
        return (
            <a
                href={`/blocks/${validator?.lastBlock?.hash}`}
                className="link"
            >
                {validator?.lastBlock?.number.toLocaleString()}
            </a>
        );
    }

    return (
        <span className="text-theme-secondary-500 dark:text-theme-dark-500">
            {validator.wallet.justMissed ? 'N/A' : 'TBD'}
        </span>
    );
}
