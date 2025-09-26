export default function DoubleChevronRight({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 12 12"
            className={className}
        >
            <path
                d="m1.102 2 3.8 3.8c.1.1.1.3 0 .4l-3.8 3.8m5-8 3.8 3.8c.1.1.1.3 0 .4l-3.8 3.8"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                fill="none"
            />
        </svg>
    );
}
