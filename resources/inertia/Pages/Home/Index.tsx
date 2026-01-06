import { Head } from "@inertiajs/react";
import Layout from "@/Layout";
import { PageProps } from "@inertiajs/core";
import { usePageMetadata } from "@/Components/General/Metadata";

export default function HomeIndex({ network: { currency } }: PageProps) {
    const metadata = usePageMetadata({
        page: "home",
        detail: {
            name: currency,
        },
    });

    return (
        <>
            <Head>{metadata}</Head>

            <Layout>{/* Content */}</Layout>
        </>
    );
}
