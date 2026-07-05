import { useApp } from '@/context/AppContext';

export function ViewTemplate () {
  const { version } = useApp();
  return (
    <div className={`flex flex-row h-full w-full items-center justify-center`}>
      View Template V{version}
    </div>
  );
}
