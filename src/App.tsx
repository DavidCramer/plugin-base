import { Header, SideBar, Tabs, type TabItem, useArchetype, Stack } from "archetype";
import * as views from "@/views";

const NAV_TABS: TabItem[] = [
	{ value: "Splash", label: "Splash Screen" },
];

export function App() {
	const { get, set } = useArchetype();
	const version = get("version", "0.0.1");
	const slug = get("slug", "plugin-base");
	const activeTab: string = get(`local.${slug}.activeTab`, "Splash");
	const ViewComponent = views[activeTab as keyof typeof views] || (() => <p>Not Found</p>);

	return (
		<Stack direction={'column'} fullHeight={true} fullWidth={true}>
			<Header title="Developers Toolkit" subtext={ version } />
			<Stack direction={'row'} fullHeight={true} fullWidth={true}>
				<SideBar size={'small'}>
					<Tabs
						items={ NAV_TABS }
						value={ activeTab }
						onChange={ (value) => {
							set(`local.${slug}.activeTab`, value)
						} }
						direction="vertical"
					/>
				</SideBar>
				<Stack direction={'column'} fullHeight={true} fullWidth={true} className={'is-bg-surface'}>
					<ViewComponent />
				</Stack>
			</Stack>
		</Stack>
	);
}
