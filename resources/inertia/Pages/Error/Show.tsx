import useShareData from "@/hooks/use-shared-data";
import Layout from "@/Layout";
import { Link } from "@inertiajs/react";
import Image401 from "@ui/images/errors/401.svg?react";
import Image403 from "@ui/images/errors/403.svg?react";
import Image404 from "@ui/images/errors/404.svg?react";
import Image419 from "@ui/images/errors/419.svg?react";
import Image429 from "@ui/images/errors/429.svg?react";
import Image503 from "@ui/images/errors/503.svg?react";
import Image500 from "@ui/images/errors/500.svg?react";
import { createElement, FunctionComponent, SVGProps } from "react";
import { useTranslation } from "react-i18next";

export default function ErrorShow({ error, status }: { error?: string; status: number }) {
    const { t } = useTranslation();
    const { isDownForMaintenance } = useShareData();

    const image: FunctionComponent<SVGProps<SVGSVGElement>> =
        {
            401: Image401,
            403: Image403,
            404: Image404,
            419: Image419,
            429: Image429,
            503: Image503,
        }[status] || Image500;

    let heading: string = t("errors.heading", { ns: "ui" });
    let message: string = t("errors.message", { ns: "ui" });

    if (status === 403) {
        heading = t("errors.403_heading", { ns: "ui" });
        message = error || t("errors.403_message", { ns: "ui" });
    }

    return (
        <Layout>
            <div className="text-center">
                <div className="mx-auto max-w-error-image">
                    {createElement(image, { className: "light-dark-icon h-full w-full" })}
                </div>

                {isDownForMaintenance ? (
                    <>
                        <h1 className="header-2 mt-8 px-2 xl:px-0">{t("errors.503_heading", { ns: "ui" })}</h1>

                        <p className="mt-4 px-8 leading-loose dark:text-theme-secondary-500">
                            {t("errors.503_message", { ns: "ui" })}
                        </p>
                    </>
                ) : (
                    <>
                        <h1 className="header-2 mt-8">{heading}</h1>

                        <p className="mt-4 leading-loose dark:text-theme-secondary-500">{message}</p>

                        <div className="mt-8 flex flex-col space-y-3 sm:flex-row sm:justify-center sm:space-x-3 sm:space-y-0">
                            <Link className="button button-secondary" href="mailto:{{ config('mail.contact_email') }}">
                                {t("actions.contact", { ns: "ui" })}
                            </Link>

                            <Link href="{{ route('home') }}" className="button button-primary">
                                {t("general.home", { ns: "ui" })}
                            </Link>
                        </div>
                    </>
                )}
            </div>
        </Layout>
    );
}
