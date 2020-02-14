import Swal from 'sweetalert2';

export default Swal
export const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});

export const SwalDelete = function (message) {
  return Swal.fire({
    title: 'Are you sure ?',
    text: `${message}`,
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'ok',
    cancelButtonText: 'cancel',
  });
}


export const SwalAccept = function (message) {
  return Swal.fire({
    title: 'Are you sure ?',
    text: `${message}`,
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'ok',
    cancelButtonText: 'cancel',

  });
}


export const SwalReject = function (message) {
  return Swal.fire({
    title: 'Are you sure ?',
    text: `${message}`,
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'ok',
    cancelButtonText: 'cancel',
  });
}