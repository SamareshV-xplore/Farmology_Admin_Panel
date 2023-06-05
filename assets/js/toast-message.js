function toast(message, duration = 3000)
{
    Toastify({
        text: message,
        duration: duration,
        gravity: "bottom",
        position: "center",
        stopOnFocus: true,
        style: {
            background: "rgba(0,0,0,0.6)"
        }
    }).showToast();
}