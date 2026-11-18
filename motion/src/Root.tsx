import { Composition } from "remotion";
import { CommercialIndustrial } from "./CommercialIndustrial";

export const RemotionRoot: React.FC = () => {
  return (
    <Composition
      id="CommercialIndustrial"
      component={CommercialIndustrial}
      durationInFrames={390}
      fps={30}
      width={1080}
      height={1080}
    />
  );
};
