import "alpinejs";
import "./vendor/ark/page-scroll";
import { picasso } from "@vechain/picasso";

window.createAvatar = (seed) =>
    picasso(seed).replace('width="100" height="100"', "");
