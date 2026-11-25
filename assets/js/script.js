// 1) Fill the shared delete dialog with the clicked item's info
var deleteModal = document.getElementById('deleteModal');
if (deleteModal) {
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        document.getElementById('deleteForm').action = button.getAttribute('data-action');
        document.getElementById('deleteId').value = button.getAttribute('data-id');
        document.getElementById('deleteName').textContent = button.getAttribute('data-name');
    });
}

// 2) Hide success/error notifications automatically after 4 seconds
document.querySelectorAll('.auto-dismiss').forEach(function (alertBox) {
    setTimeout(function () {
        bootstrap.Alert.getOrCreateInstance(alertBox).close();
    }, 4000);
});

// 3) Show a preview when choosing a product image
var imageInput = document.getElementById('image');
var imagePreview = document.getElementById('imagePreview');
if (imageInput && imagePreview) {
    imageInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            imagePreview.src = URL.createObjectURL(this.files[0]);
            imagePreview.classList.remove('d-none');
        }
    });
}
