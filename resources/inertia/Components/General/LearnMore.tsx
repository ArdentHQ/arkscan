import classNames from "classnames";
import { createElement } from "react";
import { useTranslation } from "react-i18next";

export default function LearnMore({
    url,
    icon,
    title,
    titleExtra,
    subtitle,
    padding = "py-3 px-3 mt-6 md:px-6",
    backgroundColor = "bg-theme-primary-50 dark:bg-theme-dark-blue-900 dim:bg-theme-dim-blue-950",
    borderClass,
    arrowsClass,
    iconClass = "w-11 h-11 dark:text-white text-theme-navy-600",
    mobileTall = false,
    titleColor = "text-theme-secondary-900 dark:text-white",
    subtitleColor = "text-theme-secondary-700 dark:text-theme-dark-blue-400 dim:text-theme-dark-blue-600",
    buttonColor = "dark:bg-theme-dark-blue-500! dark:hover:bg-theme-dark-blue-600! dim:bg-theme-dark-blue-600! dim:hover:bg-theme-dark-blue-700!",
}: {
    url: string;
    icon: React.FunctionComponent<React.SVGProps<SVGSVGElement>>;
    title: string;
    titleExtra?: string;
    subtitle: string;
    padding?: string;
    backgroundColor?: string;
    borderClass?: string;
    arrowsClass?: string;
    iconClass?: string;
    mobileTall?: boolean;
    titleColor?: string;
    subtitleColor?: string;
    buttonColor?: string;
}) {
    const { t } = useTranslation();

    return (
        <div
            className={classNames([
                "flex flex-col justify-between rounded-xl sm:flex-row",
                padding,
                backgroundColor,
                borderClass,
            ])}
        >
            <div
                className={classNames([
                    "mx-auto flex flex-1 items-center bg-right bg-no-repeat sm:mr-2 sm:ml-0",
                    mobileTall && "flex-col text-center sm:flex-row sm:text-left",
                    arrowsClass,
                ])}
            >
                <div>{createElement(icon, { className: iconClass })}</div>

                <div className="ml-3 flex flex-col justify-center space-y-2">
                    <span
                        className={classNames([
                            "space-x-1 text-lg leading-5.25 leading-6 font-semibold",
                            mobileTall && "mt-3 flex flex-col sm:mt-0 sm:flex-row sm:space-x-1",
                            titleColor,
                        ])}
                    >
                        <span>{title}</span>

                        {titleExtra && (
                            <span className="text-theme-secondary-600 dark:text-theme-secondary-200">{titleExtra}</span>
                        )}
                    </span>

                    <span className={classNames(["text-xs leading-3.75 font-semibold", subtitleColor])}>
                        {subtitle}
                    </span>
                </div>
            </div>

            <div className="mt-4 flex items-center sm:mt-0 sm:h-auto">
                <a
                    href={url}
                    target="_blank"
                    rel="noopener nofollow noreferrer"
                    className={classNames([
                        "button-primary flex w-full items-center rounded-lg py-3.5 sm:mt-0 sm:h-15 sm:w-auto md:mt-0 md:w-full lg:w-auto",
                        buttonColor,
                    ])}
                >
                    <div className="flex h-full items-center justify-center text-lg leading-5.25">
                        <span>{t("actions.learn_more")}</span>
                    </div>
                </a>
            </div>
        </div>
    );
}
