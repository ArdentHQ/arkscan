import { Head } from "@inertiajs/react";
import Navbar from "./Components/General/Navbar/Navbar";
import useShareData from "./hooks/use-shared-data";
import { useTranslation } from "react-i18next";

const Layout = ({ children }: { children: React.ReactNode }) => {
    const { metaPage, metaDetail = {} } = useShareData();
    const { t } = useTranslation();

    return (
        <>
            {metaPage && <Head title={t(`metatags.${metaPage}.title`, { ...metaDetail })} />}

            <Navbar />

            <div className="py-8">{children}</div>
        </>
    );
};

export default Layout;
