declare module 'aos' {
    interface AosOptions {
        duration?: number;
        easing?: string;
        once?: boolean;
        offset?: number;
        delay?: number;
        anchorPlacement?: string;
        disable?: string | boolean;
        startEvent?: string;
        animatedClassName?: string;
        initClassName?: string;
        useClassNames?: boolean;
        disableMutationObserver?: boolean;
        debounceDelay?: number;
        throttleDelay?: number;
    }

    interface AOS {
        init(options?: AosOptions): void;
        refresh(): void;
        refreshHard(): void;
    }

    const aos: AOS;
    export default aos;
}

