export default function Copy({ className = '' }: { className?: string }) {
    return (
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 16 16"
            className={className}
        >
            <path
                d="M10.656 14.066c0 .545-.466.934-.933.934H9.8 2.878c-.544 0-.933-.467-.933-.934v-8.71c0-.545.467-.934.933-.934h4.9c.234 0 .467.078.7.233l1.867 1.867c.233.233.233.467.233.7l.078 6.844Z"
                stroke="currentColor"
                strokeWidth="1.6"
                strokeLinecap="round"
                strokeLinejoin="round"
                fill="none"
            />

            <path
                d="M5.367 4.422V1.933c0-.544.466-.933.933-.933h4.9c.233 0 .467.078.7.233L13.767 3.1c.233.233.233.467.233.7v6.922c0 .545-.467.934-.933.934h-2.411"
                stroke="currentColor"
                strokeWidth="1.6"
                strokeLinecap="round"
                strokeLinejoin="round"
                fill="none"
            />
        </svg>
    );
}
