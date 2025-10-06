export default function ArrowExternal({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            className={className}
        >
            <path
                d="M18.999 6.43V1h-5.43m5.43 0L7.033 12.966M9.749 4h-7.5c-.625 0-1.25.625-1.25 1.25v12.5c0 .625.625 1.25 1.25 1.25h12.5c.625 0 1.25-.625 1.25-1.25v-7.5"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                fill="none"
            />
        </svg>
    );
}
