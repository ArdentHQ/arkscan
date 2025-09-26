export default function MagnifyingGlassSmall({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 16 16"
            className={className}
        >
            <path
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M10.504 13.904c3.28-1.36 4.8-5.2 3.44-8.4-1.36-3.28-5.2-4.72-8.4-3.44-3.2 1.28-4.8 5.2-3.44 8.4 1.44 3.28 5.12 4.8 8.4 3.44Zm2.324-1.12 1.6 1.6"
                fill="none"
            />
        </svg>
    );
}
