export default function ChevronRightSmall({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 12 12"
            className={className}
        >
            <path
                d="m4.1 2 3.8 3.8c.1.1.1.3 0 .4L4.1 10"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
            />
        </svg>
    );
}
