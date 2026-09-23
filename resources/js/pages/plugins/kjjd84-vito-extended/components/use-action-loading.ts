import { useCallback, useRef, useState } from 'react';

export function useActionLoading() {
  const loadingRef = useRef(false);
  const [isLoading, setIsLoading] = useState(false);

  const start = useCallback(() => {
    if (loadingRef.current) {
      return false;
    }

    loadingRef.current = true;
    setIsLoading(true);

    return true;
  }, []);

  const finish = useCallback(() => {
    loadingRef.current = false;
    setIsLoading(false);
  }, []);

  return { isLoading, start, finish };
}
