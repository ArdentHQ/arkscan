export default function DoubleCheckMark({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            className={className}
        >
            <path
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                d="m18 4.7-7.6 10.2c-.2.2-.4.4-.7.4-.3 0-.6-.1-.8-.3l-2-2m-4.9.1 2 2c.2.2.4.3.7.3.1 0 .3 0 .4-.1M13 4.7l-4.2 5.6"
            />
        </svg>
    );
}
