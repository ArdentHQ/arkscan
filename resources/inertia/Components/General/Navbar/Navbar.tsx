import NavbarTop from "./NavbarTop";
import NavbarDesktop from "./NavbarDesktop";

export default function Navbar() {
    return (
        <div id="navbar" className="z-30 pb-13 sm:pb-16 md:sticky md:top-0 md:pb-0">
            <NavbarTop />

            <NavbarDesktop />

            {/* @TODO: add mobile navigation (https://app.clickup.com/t/86dyeejm5) */}
        </div>
    );
}
