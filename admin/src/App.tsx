import { useEffect, useState, useCallback } from 'react';
import { useSettings } from '@/context/SettingsContext';

export function App() {

  return (
    <div className="h-[calc(100vh-32px)] overflow-hidden">

      {/* Main Container */}
      <div className="flex h-dvh">
        {/* Sidebar */}
        <div className="w-64 overflow-y-auto pb-16 bg-white border-r border-gray-200">
          <h1>
            PluginBase
          </h1>
          <div className="flex flex-col">
            {/* Sidebar content goes here */}
          </div>
        </div>
        {/* Content Area */}
        <div className="flex-1 overflow-y-auto p-8 pb-16 bg-gray-50">
          {/* Content area goes here */}
        </div>
      </div>
    </div>
  );
}
