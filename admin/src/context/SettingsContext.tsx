import { createContext, useContext, ReactNode, useState, useEffect, useCallback } from 'react';

interface Config {
    apiUrl: string;
    nonce: string;
    version: string;
    devMode: boolean;
}

// Define your plugin options here
export interface PluginOptions {
    [key: string]: unknown;
}

interface SettingsContextType {
    config: Config;
    options: PluginOptions;
    loading: boolean;
    updateOption: (key: string, value: unknown) => void;
    saveOptions: () => Promise<void>;
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
    value: Config;
}

export const SettingsProvider = ({ children, value }: SettingsProviderProps) => {
    const [options, setOptions] = useState<PluginOptions>({});
    const [loading, setLoading] = useState(true);

    const fetchClient = useCallback(async <T = unknown>(endpoint: string, data?: unknown, method: string = 'GET'): Promise<T> => {
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
    }, [value]);

    useEffect(() => {
        fetchClient<PluginOptions>('/settings')
            .then((data) => {
                setOptions(data);
            })
            .catch((err) => console.error('Failed to load settings:', err))
            .finally(() => setLoading(false));
    }, [fetchClient]);

    const updateOption = useCallback((key: string, val: unknown) => {
        setOptions(prev => ({
            ...prev,
            [key]: val
        }));
    }, []);

    const saveOptions = useCallback(async () => {
        try {
            const saved = await fetchClient<PluginOptions>('/settings', options, 'POST');
            setOptions(saved);
        } catch (e) {
            console.error('Failed to save settings:', e);
            throw e;
        }
    }, [fetchClient, options]);

    return (
        <SettingsContext.Provider value={{
            config: value,
            options,
            updateOption,
            saveOptions,
            loading,
            fetchClient
        }}>
            {children}
        </SettingsContext.Provider>
    );
};
