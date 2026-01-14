import useSharedData from "@/hooks/use-shared-data";
export default function Honeypot() {
    const { honeypot } = useSharedData();

    if (!honeypot || !honeypot.enabled) {
        return null;
    }

    return (
        <div id={`${honeypot.nameFieldName}_wrap`} style={{ display: "none" }} aria-hidden="true">
            <input
                type="text"
                value=""
                name={honeypot.nameFieldName}
                id={honeypot.nameFieldName}
                autoComplete="no"
                tabIndex={-1}
            />

            <input type="text" value={honeypot.encryptedValidFrom} name={honeypot.validFromFieldName} />
        </div>
    );
}
