export interface SocialNetworkUrls {
    twitter: string;
    github: string;
}

export interface SubjectOption {
    value: string;
    label: string;
}

export interface SupportProps {
    socialNetworkUrls: SocialNetworkUrls;
    subjects: SubjectOption[];
}
