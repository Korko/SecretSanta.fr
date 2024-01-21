export function throttle(func, delay = 0) {
    let wait = false;

    return (...args) => {
      if (wait) {
          return;
      }

      wait = true;
      func(...args);
      setTimeout(() => {
        wait = false;
      }, delay);
    }
  }
