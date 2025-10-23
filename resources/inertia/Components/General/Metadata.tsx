import { ReactElement } from "react";
import { useTranslation } from "react-i18next";

export function buildMetadataTags({
    title,
    description,
    image,
}: {
    title: string;
    description: string;
    image?: string;
}): ReactElement[] {
    const tags: ReactElement[] = [
        <title key="title">{title}</title>,

        <meta key="desc" name="description" content={description} />,

        <meta key="og:title" property="og:title" content={title} />,
        <meta key="og:description" property="og:description" content={description} />,
    ];

    if (image) {
        tags.push(<meta key="og:image" property="og:image" content={image} />);
    }

    return tags;
}

export function usePageMetadata({ page, detail = {} }: { page: string; detail?: Record<string, string | number> }) {
    const { t } = useTranslation();

    let imageTag: string | undefined = t(`metatags.${page}.image`, { ...detail });
    if (imageTag === `metatags.${page}.image`) {
        imageTag = undefined;
    }

    return buildMetadataTags({
        title: t(`metatags.${page}.title`, { ...detail }),
        description: t(`metatags.${page}.description`, { ...detail }),
        image: imageTag,
    });
}
