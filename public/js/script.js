document.onreadystatechange = function () {
    if (document.readyState == "interactive") {
        (function () {
            const
                taskLink = document.querySelectorAll('.checktable__link'),
                taskDescriptionAll = document.querySelectorAll('.checktable__description'),

                slideIn = (el) => {
                    el.style.height = `${el.scrollHeight}px`;
                },

                slideOut = (el) => {
                    el.style.height = `${el.scrollHeight}px`;
                    window.getComputedStyle(el, null).getPropertyValue("height");
                    el.style.height = "0";
                };
            console.dir(taskLink);
            for (let item = 0; item < taskLink.length; item++) {
                taskLink[item].addEventListener('click', taskToogle);
            }

            function taskToogle() {
                event.preventDefault();
                let $this = this,
                    taskDescription = $this.closest('.checktable__task').querySelectorAll('.checktable__description');
                if ($this.classList.contains('checktable__link-active')) {
                    slideOut(taskDescription[0], 200);
                    $this.classList.remove('checktable__link-active');
                    console.dir(taskDescription[0]);
                } else {
                    for (let i = 0; i < taskLink.length; i++) {
                        slideOut(taskDescriptionAll[i], 200);
                        taskLink[i].classList.remove('checktable__link-active');
                    }
                    slideIn(taskDescription[0], 0);
                    $this.classList.add('checktable__link-active');
                }

            }
        }());
    }
}