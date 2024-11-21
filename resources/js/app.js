import "./bootstrap";
import Chat from "./chat";
import Search from "./live-search";
import Profile from "./profile";

// Load search only if page contains Search icon
if (document.querySelector(".header-search-icon")) {
    // new Search();
}

// Load search only if page contains CHat icon
if (document.querySelector(".header-chat-icon")) {
    // new Chat();
}

// Load (profile, followers, following) only if links on Profile
if (document.querySelector(".profile-nav")) {
    new Profile();
}
