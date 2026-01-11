/**
 * Notification utilities using Vue Toast Notification and SweetAlert2
 */

import { useToast } from 'vue-toast-notification';
import Swal from 'sweetalert2';

/**
 * Show a success toast notification
 */
export function showSuccess(message: string): void {
    const toast = useToast();
    toast.success(message, {
        position: 'top-right',
        duration: 3000,
    });
}

/**
 * Show an error toast notification
 */
export function showError(message: string): void {
    const toast = useToast();
    toast.error(message, {
        position: 'top-right',
        duration: 4000,
    });
}

/**
 * Show an info toast notification
 */
export function showInfo(message: string): void {
    const toast = useToast();
    toast.info(message, {
        position: 'top-right',
        duration: 3000,
    });
}

/**
 * Show a warning toast notification
 */
export function showWarning(message: string): void {
    const toast = useToast();
    toast.warning(message, {
        position: 'top-right',
        duration: 3000,
    });
}

/**
 * Show a default toast notification
 */
export function showDefault(message: string): void {
    const toast = useToast();
    toast.open({
        message,
        position: 'top-right',
        duration: 3000,
    });
}

/**
 * Show a confirmation dialog using SweetAlert2 with Bootstrap styling
 */
export async function confirmAction(
    title: string,
    text: string,
    confirmButtonText = 'Yes, proceed',
    cancelButtonText = 'Cancel',
): Promise<boolean> {
    const result = await Swal.fire({
        title,
        text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText,
        cancelButtonText,
        confirmButtonColor: '#a01d62', // Score Beyond primary
        cancelButtonColor: '#6c757d', // Bootstrap secondary
        buttonsStyling: true,
        customClass: {
            popup: 'rounded-4',
            confirmButton: 'btn btn-primary rounded-pill px-4',
            cancelButton: 'btn btn-secondary rounded-pill px-4',
        },
    });

    return result.isConfirmed;
}

/**
 * Show a success dialog using SweetAlert2
 */
export function showSuccessDialog(title: string, text?: string): Promise<void> {
    return Swal.fire({
        title,
        text,
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#a01d62', // Score Beyond primary
        buttonsStyling: true,
        customClass: {
            popup: 'rounded-4',
            confirmButton: 'btn btn-primary rounded-pill px-4',
        },
    }).then(() => undefined);
}

/**
 * Show an error dialog using SweetAlert2
 */
export function showErrorDialog(title: string, text?: string): Promise<void> {
    return Swal.fire({
        title,
        text,
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#ea4335',
        buttonsStyling: true,
        customClass: {
            popup: 'rounded-4',
            confirmButton: 'btn btn-danger rounded-pill px-4',
        },
    }).then(() => undefined);
}

