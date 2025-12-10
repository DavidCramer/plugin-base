import { createContext, useContext, ReactNode } from 'react';

interface SettingsData {
    apiUrl: string;
    nonce: string;
    version: string;
    devMode: boolean;
}

interface SettingsContextType {
    settings: SettingsData;
    fetchClient: <T = unknown>(endpoint: string, data?: unknown, method?: string) => Promise<T>;
}

const SettingsContext = createContext<SettingsContextType | undefined>(undefined);

// eslint-disable-next-line react-refresh/only-export-components
export const useSettings = () => {
    const context = useContext(SettingsContext);
    if (!context) {
        throw new Error('useSettings must be used within a SettingsProvider');
    }
    return context;
};

interface SettingsProviderProps {
    children: ReactNode;
    value: SettingsData;
}

export const SettingsProvider = ({ children, value }: SettingsProviderProps) => {

    const fetchClient = async <T = unknown>(endpoint: string, data?: unknown, method: string = 'GET'): Promise<T> => {
        const url = `${value.apiUrl}${endpoint}`;

        const headers: HeadersInit = {
            'X-WP-Nonce': value.nonce,
            'Content-Type': 'application/json',
        };

        const config: RequestInit = {
            method,
            headers,
        };

        if (data) {
            config.body = JSON.stringify(data);
        }

        const response = await fetch(url, config);

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `Request failed with status ${response.status}`);
        }

        return response.json();
    };

    return (
        <SettingsContext.Provider value={{ settings: value, fetchClient }}>
            {children}
        </SettingsContext.Provider>
    );
};
