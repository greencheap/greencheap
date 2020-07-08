// JavaScript
(function (UIkit) {
    const { util } = UIkit;
    const { $ } = util;
    const { attr } = util;
    const { css } = util;
    const { toNodes } = util;
    const { addClass } = util;
    const { removeClass } = util;

    util.on(window, 'load', () => {
        const login = $('.js-login');
        const messages = $('.pk-system-messages');

        if (messages && messages.children.length) {
            const children = toNodes(messages.children);

            addClass(login, 'uk-animation-shake');
            login.focus();

            children.forEach((el) => {
                const message = $(el);
                setTimeout(() => {
                    UIkit.alert(message).close();
                    removeClass(login, 'uk-animation-shake');
                }, 3500);
            });
        }
    });
}(UIkit));
