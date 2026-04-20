import { Head } from "@inertiajs/react";
import Navbar from "./Components/General/Navbar/Navbar";
import useShareData from "./hooks/use-shared-data";
import { useTranslation } from "react-i18next";
import FlashToastListener from "./Providers/Toast/FlashToastListener";

const Layout = ({ children, className = "py-8" }: { children: React.ReactNode; className?: string }) => {
    const { metaPage, metaDetail = {} } = useShareData();
    const { t } = useTranslation();

    return (
        <>
            {metaPage && <Head title={t(`metatags.${metaPage}.title`, { ...metaDetail })} />}

            <FlashToastListener />

            <Navbar />

            <main className={className}>{children}</main>
        </>
    );
};

export default Layout;
