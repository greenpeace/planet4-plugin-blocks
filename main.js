$(document).ready(function(){"use strict";const t=$(".back-top");$(".submenu-block").length>0&&($(window).scroll(function(){$(this).scrollTop()>400?(t.fadeIn(),$(".cookie-block:visible").length>0?t.css("bottom","120px"):t.css("bottom","50px")):t.fadeOut()}),t.click(function(){return $("body, html").animate({scrollTop:0},800),!1}))}),$(document).ready(function(){"use strict";$(".article-listing").off("mouseenter").on("mouseenter",".article-list-item-image",function(){$(".article-list-item-headline",$(this).parent()).addClass("article-hover")}).off("mouseleave").on("mouseleave",".article-list-item-image",function(){$(".article-list-item-headline",$(this).parent()).removeClass("article-hover")}),$(".four-column-content-symbol").hover(function(){$("h4",$(this).parent()).addClass("four-column-hover")},function(){$("h4",$(this).parent()).removeClass("four-column-hover")})}),$(document).ready(function(){"use strict";var t=$(".block-wide"),e=$("div.page-template, div.container").eq(0);function o(){var o=e.width();t.each(function(){var t=$(this).innerWidth(),e=(o-t)/2;"rtl"===$("html").attr("dir")?($(this).css("margin-left","auto"),$(this).css("margin-right",e+"px")):$(this).css("margin-left",e+"px")})}t.length>0&&e.length>0?(o(),$(window).on("resize",o)):($(".block-wide").attr("style","margin: 0px !important;padding-left: 0px !important;padding-right: 0px !important"),$("iframe").attr("style","left: 0"))}),$(document).ready(function(){"use strict";$(".can-do-steps .col").hover(function(){$(this).find(".step-number").toggleClass("active")})}),$(document).ready(function(){"use strict";$("#carousel-wrapper-header").find("img").on("load",function(){$(this).get(0).currentSrc!==$(this).parent().css("background-image").replace(/.*\s?url\(['"]?/,"").replace(/['"]?\).*/,"")&&$(this).parent().css("background-image","url("+$(this).get(0).currentSrc+")")});var t=1e3,e=$("#carousel-wrapper-header"),o=e.find(".carousel-indicators"),n=e.find(".carousel-item"),s=null;function a(t){var e=$(t),o=e.next(".carousel-item");return o.length?o:e.prevAll(".carousel-item").last()}function i(t){o.children().each(function(e,o){$(o).toggleClass("active",e===t)})}function r(){var e=n.filter(".active"),o=a(e).addClass("next");if(s)return clearTimeout(s),s=null,n.removeClass("fade-out slide-over active next"),o.addClass("active"),a(o).addClass("next"),void r();e.addClass("slide-over"),i(n.index(o)),s=setTimeout(function(){e.addClass("fade-out"),s=setTimeout(function(){e.removeClass("active"),n.removeClass("slide-over fade-out"),o.removeClass("next").addClass("active"),a(o).addClass("next"),s=null},t/2)},t)}function c(){var t,e,o=n.filter(".active");l((t=$(o),(e=t.prev(".carousel-item")).length?e:t.nextAll(".carousel-item").last()).data("slide"))}function l(t){var e=n.eq(t);e.hasClass("active")&&!e.hasClass("slide-over")||(e.hasClass("next")?r():(s&&clearTimeout(s),i(t),n.removeClass("active next slide-over fade-out"),e.addClass("active"),a(e).addClass("next")))}if(n.each(function(t,e){var n=$(e);$("<li>").attr("data-target","#carousel-wrapper-header").attr("data-slide-to",t).toggleClass("active",0===t).appendTo(o);var s=n.find("img"),i=a(n).find("img"),r=s.get(0).currentSrc||s.attr("src");if(n.css("background-image","url("+r+")").css("background-position",s.data("background-position")),i.length>0){var c=$("<div>").addClass("carousel-preview-wrap").prependTo(n),l=i.get(0).currentSrc||i.attr("src");$("<div>").addClass("carousel-preview").css("background-image","url("+l+")").css("background-position",i.data("background-position")).appendTo(c)}n.attr("data-slide",t)}),e.on("click",".carousel-control-next",function(t){t.preventDefault(),r()}).on("click",".carousel-indicators li",function(t){t.preventDefault(),l($(t.target).data("slide-to"))}),$(".carousel-header").length>0){var d=$(".carousel-header")[0],u=new Hammer(d,{recognizers:[]}),p=new Hammer.Manager(u.element),h=new Hammer.Swipe;p.add(h),p.on("swipeleft",function(){r()}),p.on("swiperight",function(){c()}),p.on("swipeup",function(t){var e=$(window).scrollTop();t.preventDefault(),$("html, body").animate({scrollTop:e+200})}),p.on("swipedown",function(t){var e=$(window).scrollTop();t.preventDefault(),$("html, body").animate({scrollTop:e-200})})}}),$(document).ready(function(){"use strict";function t(){if($("#happy-point > iframe").length>0)return window.removeEventListener("load",t),window.removeEventListener("resize",t),void window.removeEventListener("scroll",t);$("#happy-point")[0].getBoundingClientRect().top<window.innerHeight&&$("#happy-point").append($("<iframe></iframe>").attr({src:decodeURIComponent(e),cellSpacing:"0",allowtransparency:"true",frameborder:"0",scrolling:"no",width:"100%"}))}const e=$("#happy-point").data("src");e&&(window.addEventListener("load",t),window.addEventListener("resize",t),window.addEventListener("scroll",t))}),$(document).ready(function(){$(".four-column-content").each(function(){var t=$(".post-column:visible",$(this)).length;0==t%4?$(this).attr("data-posts_per_row",4):0==t%3&&$(this).attr("data-posts_per_row",3)}),$(".btn-load-more-posts-click").off("mousedown touchstart").on("mousedown touchstart",function(t){t.preventDefault();var e=$(".post-column:hidden",$(this).closest(".container")),o=$(this).closest(".four-column-content").data("posts_per_row");e.length>0&&e.slice(0,o).show("slow"),e.length<=o&&$(this).closest(".load-more-posts-button-div").hide("fast")}),$(".covers-block").each(function(){var t=$(".cover-card-column:visible",$(this)).length;0==t%3?$(this).attr("data-covers_per_row",3):0==t%2&&$(this).attr("data-covers_per_row",2)}),$(".btn-load-more-covers-click").off("mousedown touchstart").on("mousedown touchstart",function(t){t.preventDefault();var e=$(".cover-card-column:hidden",$(this).closest(".container")),o=$(this).closest(".covers-block").data("covers_per_row");e.length>0&&e.slice(0,o).show("slow"),e.length<=o&&$(this).closest(".load-more-covers-button-div").hide("fast")}),$(".btn-load-more-articles-click").off("mousedown touchstart").on("mousedown touchstart",function(t){t.preventDefault();var e=$(".article-list-item.d-none",$(this).closest(".container"));e.length>0&&($(".article-list-item.d-none img").slice(0,3).each(function(){var t=this;t.setAttribute("src",t.getAttribute("data-src")),t.onload=function(){t.removeAttribute("data-src")}}),e.slice(0,3).removeClass("d-none").fadeOut(0).slideDown("slow"));0===(e=$(".article-list-item.d-none",$(this).closest(".container"))).length&&$(this).closest(".load-more-articles-button-div").hide("fast")}),$(".load-more").off("mousedown touchstart").on("mousedown touchstart",function(t){t.preventDefault();const e=$(this.dataset.content,$(this).closest("section")),o=parseInt(this.dataset.page)+1,n=parseInt(this.dataset.total_pages),s=p4_vars.ajaxurl+`?page=${o}`;this.dataset.page=o,$.ajax({url:s,type:"GET",data:{action:"load_more",args:this.dataset,_wpnonce:$("#_wpnonce").val()},dataType:"html"}).done(function(t){e.append(t),o===n&&$(this).fadeOut()}).fail(function(t,e,o){console.log(o)})}),$(".btn-load-more-campaigns-click").off("mousedown touchstart").on("mousedown touchstart",function(t){t.preventDefault();var e=$(".campaign-card-column:hidden",$(this).closest(".container"));e.length>0&&e.slice(0,3).show("slow"),e.length<=3&&$(this).closest(".load-more-campaigns-button-div").hide("fast")})}),$(document).ready(function(){"use strict";var t=$(".post-content").find("> #action-card"),e=t.offset(),o=100;function n(){if($(window).width()>992){let n=$(".post-details > p:last-child").offset().top-t.outerHeight()-o;$(window).scrollTop()>e.top&&$(window).scrollTop()<n&&t.stop().animate({marginTop:$(window).scrollTop()-e.top+o}),$(window).scrollTop()<e.top&&t.stop().animate({marginTop:0})}else t.css("margin-top",0)}t.length>0&&(window.addEventListener("scroll",n),window.addEventListener("resize",n))}),$(document).ready(function(){"use strict";$(".four-column-content").each(function(){var t=$(".publications-slider .post-column",$(this)).length;t>3&&$(window).width()<768&&$(".publications-slider").slick({infinite:!1,mobileFirst:!0,slidesToShow:2.2,slidesToScroll:1,arrows:!1,dots:!1,responsive:[{breakpoint:992,settings:{slidesToShow:4}},{breakpoint:768,settings:{slidesToShow:3}},{breakpoint:576,settings:{slidesToShow:2}}]}),t<4&&$(window).width()>768&&$(".post-column",$(this)).removeClass("col-lg-3").removeClass("col-md-4").addClass("col-md")})});
//# sourceMappingURL=main.js.map
