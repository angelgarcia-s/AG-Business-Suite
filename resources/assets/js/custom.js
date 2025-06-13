(function () {
  "use strict";

  /* page loader */
  function hideLoader() {
    const loader = document.getElementById("loader");
    loader.classList.add("!hidden");
  }
  window.addEventListener("load", hideLoader);
  /* page loader */

  /* footer year */
  document.getElementById("year").innerHTML = new Date().getFullYear();
  /* footer year */
  /*Start Sidemenu Scroll */
  var myElement = document.getElementById("sidebar-scroll");
  new SimpleBar(myElement, { autoHide: true });
  /*End Sidemenu Scroll */

  /*Start Sidemenu Scroll */
  var myElement = document.getElementById("header-notification-scroll");
  new SimpleBar(myElement, { autoHide: true });
  /*End Sidemenu Scroll */

  /* header dropdowns scroll */
  var myHeaderShortcut = document.getElementById("header-shortcut-scroll");
  new SimpleBar(myHeaderShortcut, { autoHide: true });
  /*End header dropdowns scroll */
  /* Choices JS */
  document.addEventListener("DOMContentLoaded", function () {
    var genericExamples = document.querySelectorAll("[data-trigger]");
    for (let i = 0; i < genericExamples.length; ++i) {
      var element = genericExamples[i];
      new Choices(element, {
        allowHTML: true,
        placeholderValue: "This is a placeholder set in the config",
        searchPlaceholderValue: "Search",
      });
    }
  });
  /* Choices JS */

  /* box with close button */
  let DIV_Box = ".box";
  let boxRemoveBtn = document.querySelectorAll(".box-remove");
  boxRemoveBtn.forEach((ele) => {
    ele.addEventListener("click", function (e) {
      e.preventDefault();
      let $this = this;
      let box = $this.closest(DIV_Box);
      box.remove();
      return false;
    });
  });
  /* box with close button */

  /* box with fullscreen */
  let boxFullscreenBtn = document.querySelectorAll(".box-fullscreen");
  boxFullscreenBtn.forEach((ele) => {
    ele.addEventListener("click", function (e) {
      let $this = this;
      let box = $this.closest(DIV_Box);
      box.classList.toggle("box-fullscreen");
      box.classList.remove("box-collapsed");
      e.preventDefault();
      return false;
    });
  });
  /* box with fullscreen */ /* back to top */

  /* card with close button */

  const scrollToTop = document.querySelector(".scrollToTop");
  const $rootElement = document.documentElement;
  const $body = document.body;
  window.onscroll = () => {
    const scrollTop = window.scrollY || window.pageYOffset;
    const clientHt = $rootElement.scrollHeight - $rootElement.clientHeight;
    if (window.scrollY > 100) {
      scrollToTop.style.display = "flex";
    } else {
      scrollToTop.style.display = "none";
    }
  };
  scrollToTop.onclick = () => {
    window.scrollTo(0, 0);
    // window.scrollTo({ top: 0, behavior: 'smooth' });
  };
  /* back to top */

  /*header-remove */
  const headerbtn2 = document.querySelectorAll(".header-remove-btn");

  headerbtn2.forEach((button, index) => {
    button.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      button.parentNode.remove();
      if (document.getElementById("allCartsContainer")) {
        document.getElementById("cart-data").innerText = `${
          document.getElementById("allCartsContainer").children.length
        } Items`;
        document.getElementById("cart-data2").innerText = `${
          document.getElementById("allCartsContainer").children.length
        }`;
      }
      if (document.getElementById("allNotifyContainer")) {
        document.getElementById("notify-data").innerText = `${
          document.getElementById("allNotifyContainer").children.length
        }`;
      }

      if (document.getElementById("allCartsContainer")) {
        if (document.getElementById("allCartsContainer").children.length == 0) {
          document.getElementById("allCartsContainer").parentNode.innerHTML = `
                        <div class="p-6 pb-8 text-center">
                          <div>
                            <i class="ri ri-shopping-cart-2-line leading-none text-4xl avatar avatar-lg bg-primary/20 text-primary rounded-full p-3 align-middle flex justify-center mx-auto"></i>
                            <div class="mt-5">
                              <p class="text-base font-semibold text-gray-800 dark:text-white mb-1">
                                No Items In Cart
                              </p>
                              <p class="text-xs text-gray-500 dark:text-white/70">
                              When you have Items added here , they will appear here.
                              </p>
                              <a href="javascript:void(0);" class="m-0 ti-btn ti-btn-primary py-1 mt-5"><i class="ti ti-arrow-right text-base leading-none"></i>Continue Shopping</a>
                            </div>
                          </div>
                        </div>`;
        }
      }
      if (document.getElementById("allNotifyContainer")) {
        if (
          document.getElementById("allNotifyContainer").children.length == 0
        ) {
          document.getElementById("allNotifyContainer").parentNode.innerHTML = `
          <div class="p-6 pb-8 text-center">
          <div>
            <i class="ri ri-notification-off-line leading-none text-4xl avatar avatar-lg bg-secondary/20 text-secondary rounded-full p-3 align-middle flex justify-center mx-auto"></i>
            <div class="mt-5">
              <p class="text-base font-semibold text-gray-800 dark:text-white mb-1">
                No Notifications Found
              </p>
              <p class="text-xs text-gray-500 dark:text-white/70">
              When you have notifications added here , they will appear here.
              </p>
            </div>
          </div>
        </div>`;
        }
      }
    });
  });
  /*header-remove */

  /* for cart dropdown */
  const headerbtn = document.querySelectorAll(".dropdown-item-close");
  headerbtn.forEach((button) => {
    button.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      button.parentNode.parentNode.parentNode.parentNode.parentNode.remove();
      document.getElementById("cart-data").innerText = `${
        document.querySelectorAll(".dropdown-item-close").length
      } Items`;
      document.getElementById("cart-icon-badge").innerText = `${
        document.querySelectorAll(".dropdown-item-close").length
      }`;
      console.log(
        document.getElementById("header-cart-items-scroll").children.length
      );
      if (document.querySelectorAll(".dropdown-item-close").length == 0) {
        let elementHide = document.querySelector(".empty-header-item");
        let elementShow = document.querySelector(".empty-item");
        elementHide.classList.add("hidden");
        elementShow.classList.remove("hidden");
      }
    });
  });
  /* for cart dropdown */

/* toggle switches */
let customSwitch = document.querySelectorAll(".toggle");
customSwitch.forEach((e) =>
  e.addEventListener("click", () => {
    e.classList.toggle("on");
  })
);
/* toggle switches */

  /* for notifications dropdown */
  const headerbtn1 = document.querySelectorAll(".dropdown-item-close1");
  headerbtn1.forEach((button) => {
    button.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      button.parentNode.parentNode.parentNode.parentNode.remove();
      document.getElementById("notifiation-data").innerText = `${
        document.querySelectorAll(".dropdown-item-close1").length
      } Unread`;
      document.getElementById("notification-icon-badge").innerText = `${
        document.querySelectorAll(".dropdown-item-close1").length
      }`;
      if (document.querySelectorAll(".dropdown-item-close1").length == 0) {
        let elementHide1 = document.querySelector(".empty-header-item1");
        let elementShow1 = document.querySelector(".empty-item1");
        elementHide1.classList.add("hidden");
        elementShow1.classList.remove("hidden");
      }
    });
  });
  /* for notifications dropdown */
})();

/* full screen */
var elem = document.documentElement;
window.openFullscreen = function() {
  let open = document.querySelector(".full-screen-open");
  let close = document.querySelector(".full-screen-close");

  if (
    !document.fullscreenElement &&
    !document.webkitFullscreenElement &&
    !document.msFullscreenElement
  ) {
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) {
      /* Safari */
      elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
      /* IE11 */
      elem.msRequestFullscreen();
    }
    close.classList.add("d-block");
    close.classList.remove("d-none");
    open.classList.add("d-none");
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.webkitExitFullscreen) {
      /* Safari */
      document.webkitExitFullscreen();
      console.log("working");
    } else if (document.msExitFullscreen) {
      /* IE11 */
      document.msExitFullscreen();
    }
    close.classList.remove("d-block");
    open.classList.remove("d-none");
    close.classList.add("d-none");
    open.classList.add("d-block");
  }
}

/* count-up */
var i = 1;
setInterval(() => {
  document.querySelectorAll(".count-up").forEach((ele) => {
    if (ele.getAttribute("data-count") >= i) {
      i = i + 1;
      ele.innerText = i;
    }
  });
}, 10);
/* count-up */
