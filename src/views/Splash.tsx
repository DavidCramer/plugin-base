import { Stack, useArchetype } from "archetype";

export function Splash() {

	const { get } = useArchetype();
	const data = get('');
	return (
		<Stack>
			<pre>{ JSON.stringify(data, null, 2) }</pre>
		</Stack>
	);
}
